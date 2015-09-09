# Run by typing : R --vanilla --args expressionfile=~/location/ agefile=~/location/ formula=FORMULA-GENERAL-PREDICTOR-GENE_ID.txt outputtxt=OUTCOME-GENERAL-PREDICTOR-GENE_ID.txt < GENERAL-PREDICTION.R

# print command line args

library(preprocessCore)
commandArgs(trailingOnly=TRUE)
options(expressions=30000)

# process command line arguments
for (e in commandArgs(trailingOnly=TRUE)) {
  ta = strsplit(e,"=",fixed=TRUE)
  if(!is.null(ta[[1]][2])) {
    assign(ta[[1]][1],ta[[1]][2])
  } else {
    assign(ta[[1]][1],TRUE)
  }
}

expressionfile		# input expression file
agefile			# input age file
formula			# prediction formula
outputtxt			# results file: association of delta age with phenotypes of interest

expression <- read.table(expressionfile, header = TRUE, row.names = 1, stringsAsFactors=FALSE)
dim(expression)

nPrbs <- dim(expression)[1]
nSamples <- dim(expression)[2]
nPrbs
nSamples


oexpression <- expression[order(rownames(expression)),]

#genes_needed <- read.table("/home/marijn/workspace/tragca/backend/GENEID.txt", header = FALSE, stringsAsFactors=FALSE)
genes_needed <- read.table("/trap/backend/GENEID.txt", header = FALSE, stringsAsFactors=FALSE)

k <- rownames(oexpression)
l <- genes_needed[,1]

genes.present <- genes_needed[l %in% k,]
genes.notpresent <- genes_needed[!l %in% k,]

ngenes.notpresent <- length(genes.notpresent)
missing.genes=matrix(0,ngenes.notpresent,nSamples)
rownames(missing.genes) = genes.notpresent
colnames(missing.genes) = colnames(oexpression)
all.genes <- rbind(oexpression,missing.genes)

m=order(row.names(all.genes))
all.genes = all.genes[m,]

#myGENES <- cbind(GENEID = rownames(all.genes), all.genes)
#rownames(myGENES) <- NULL
#write.table(myGENES, "FULL-GENE-RES-MATRIX.txt", , quote=FALSE, row.names=F, sep="\t")

# Transpose Gene Expression Data
t.all.genes <- t(all.genes)
dim(t.all.genes)

# Load AGE file
covariates <- read.table(agefile, header = TRUE, row.names = 1, stringsAsFactors=FALSE)

covariates_selection = covariates[row.names(t.all.genes),]
length(covariates_selection)

nSamples <- dim(t.all.genes)[1]
nPrbs <- dim(t.all.genes)[2]
nSamples
nPrbs

results=matrix(NA,nSamples,3)
colnames(results) = c("CA","TA","DA")
rownames(results) = rownames(t.all.genes)

selection = as.data.frame(t.all.genes)

# Load the formula - calculated using the prediction algorithm
age.predicted <- matrix(NA,nSamples, 1)

myModel = readLines(formula)
myModel = eval(parse(text=paste(myModel)))
age.predicted = myModel

rownames(results) = rownames(t.all.genes)

predictor <- age.predicted
chron_age <- covariates_selection

length(predictor)
length(chron_age)

mean(predictor)
sd(predictor)

mean(chron_age)
sd(chron_age)


pred_age = ( mean(chron_age) - mean(predictor)*sd(chron_age) / sd(predictor)) + (predictor*sd(chron_age)/sd(predictor))
delta_age = pred_age - chron_age

mean(pred_age)
sd(pred_age)

mean(delta_age)
sd(delta_age)

results[,1] = chron_age
results[,2] = pred_age
results[,3] = delta_age

myDF <- cbind(SAMPLEID = rownames(results), results)
rownames(myDF) <- NULL
write.table(myDF, outputtxt, quote=FALSE, row.names=F, sep="\t")





